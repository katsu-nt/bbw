import React, { useEffect, useState, useRef } from "react";
import { useSelector, useDispatch } from "react-redux";
import { fetchExchangeCurrent } from "@/store/market/exchangeSlice";
import {
  DropdownMenu,
  DropdownMenuTrigger,
  DropdownMenuContent,
  DropdownMenuItem,
} from "@/components/ui/dropdown-menu";
import { ChevronDown, Repeat2 } from "lucide-react";

const currencyOptions = [
  { code: "USD", name: "USD", flag: "/images/upload/united-states.png" },
  { code: "VND", name: "VND", flag: "/images/upload/vietnam.png" },
  { code: "EUR", name: "EUR", flag: "/images/upload/european-union.png" },
  { code: "JPY", name: "JPY", flag: "/images/upload/japan.png" },
  { code: "CNY", name: "CNY", flag: "/images/upload/china.png" },
  { code: "AUD", name: "AUD", flag: "/images/upload/australia.png" },
];

function formatAmount(val) {
  if (typeof val !== "number" || isNaN(val)) return "";
  // Format ngăn cách bằng . giống Việt Nam (tuỳ yêu cầu)
  return val.toLocaleString("vi-VN");
}

function InputCurrencyField({
  value,
  onValueChange,
  currency,
  onSelectCurrency,
  disabled,
  readOnly,
}) {
  // Focus input khi click vào vùng input, không dropdown
  const inputRef = useRef();

  return (
    <div className="flex items-center w-full max-h-[44px] rounded-lg px-2 py-0 bg-white focus-within:border-[#181D27] transition border rounded-md border-[#E7E7E7] shadow overflow-hidden">
      <input
        ref={inputRef}
        type="text"
        pattern="[0-9.,]*"
        inputMode="decimal"
        disabled={disabled}
        readOnly={readOnly}
        className={
          "flex-1 py-3 px-2 text-base font-medium outline-none border-none bg-transparent text-[#181D27]"
        }
        value={value}
        onChange={(e) => {
          let v = e.target.value.replace(/\./g, "").replace(/,/g, "");
          if (v === "") onValueChange("");
          else onValueChange(Number(v));
        }}
        onFocus={(e) => e.target.select()}
      />
      <DropdownMenu>
        <DropdownMenuTrigger asChild>
          <button
            type="button"
            className="flex items-center gap-2 py-1 pl-3 pr-2 rounded-md select-none hover:bg-gray-100 focus:bg-gray-100 focus:outline-none"
            tabIndex={0}
            style={{ minWidth: 70 }}
          >
            <img
                src={currency.flag}
                alt="Việt Nam"
                className="inline-block w-6 h-6 rounded-full"
              />
            <span className="font-semibold text-base ml-1">
              {currency.code}
            </span>
            <ChevronDown className="w-4 h-4 ml-1 text-[#888]" />
          </button>
        </DropdownMenuTrigger>
        <DropdownMenuContent
          align="end"
          sideOffset={4}
          className="min-w-[120px] rounded-md border z-[9999] bg-white shadow-lg"
          style={{
            maxHeight: 180,
            overflowY: "auto",
            scrollbarWidth: "thin",
            scrollbarColor: "#ECECEC transparent",
          }}
        >
          <style>{`
    [data-slot="dropdown-menu-content"]::-webkit-scrollbar { width: 6px; }
    [data-slot="dropdown-menu-content"]::-webkit-scrollbar-thumb { background-color: #ececec; border-radius: 4px; }
    [data-slot="dropdown-menu-content"]::-webkit-scrollbar-button { display: none; height: 0; }
  `}</style>
          {currencyOptions.map((c) => (
            <DropdownMenuItem
              key={c.code}
              onClick={() => {
                onSelectCurrency(c.code);
                setTimeout(() => inputRef.current?.focus(), 100);
              }}
              disabled={c.code === currency.code}
              className="flex gap-3 items-center px-4 py-1 font-medium text-base hover:bg-gray-100 hover:outline-none hover:cursor-pointer hover:shadow focus:outline-none"
              style={{ minHeight: 60 }}
            >
              <img
                src={c.flag}
                alt="Việt Nam"
                className="inline-block w-6 h-6 rounded-full"
              />
              <span className="font-medium">{c.code}</span>
            </DropdownMenuItem>
          ))}
        </DropdownMenuContent>
      </DropdownMenu>
    </div>
  );
}

export default function CurrencyConverter() {
  const [from, setFrom] = useState("USD");
  const [to, setTo] = useState("VND");
  const [amount, setAmount] = useState(1000);
  const dispatch = useDispatch();

  const key = `${from}-${to}`;
  const exchange = useSelector((state) => state.exchange.current[key]);
  const loading = useSelector((state) => state.exchange.loading.current);

  useEffect(() => {
    if (from !== to) {
      dispatch(
        fetchExchangeCurrent({ base_currency: from, quote_currency: to })
      );
    }
  }, [dispatch, from, to]);

  const handleReverse = () => {
    setFrom(to);
    setTo(from);
  };

  let result = "";
  if (exchange && typeof exchange.rate === "number" && !loading) {
    result = amount * exchange.rate;
  }

  return (
    <div className="bg-white py-2 rounded-t-md flex flex-col gap-2 mb-4">
      <div className="font-medium text-lg mb-1 pl-1">
        Chuyển đổi tiền tệ
      </div>

      {/* FROM field */}
      <InputCurrencyField
        value={formatAmount(amount)}
        onValueChange={setAmount}
        currency={currencyOptions.find((x) => x.code === from)}
        onSelectCurrency={setFrom}
        disabled={false}
        readOnly={false}
      />

      {/* Swap icon */}
      <div className="flex items-center justify-center my-0">
        <button
          onClick={handleReverse}
          className="rounded-full border border-[#E7E7E7] bg-white p-2 flex items-center shadow hover:bg-gray-100 transition"
          aria-label="Đổi chiều"
        >
          <Repeat2 className="w-4 h-4 text-[#191919]" />
        </button>
      </div>

      {/* TO field */}
      <InputCurrencyField
        value={
          loading
            ? "..."
            : result !== "" && !isNaN(result)
            ? formatAmount(Math.round(result))
            : ""
        }
        onValueChange={() => {}} // Không cho user nhập vào result
        currency={currencyOptions.find((x) => x.code === to)}
        onSelectCurrency={setTo}
        disabled={false}
        readOnly={true}
      />
    </div>
  );
}
