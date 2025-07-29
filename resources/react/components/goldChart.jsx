"use client";

import { useState, useEffect } from "react";
import { useDispatch, useSelector } from "react-redux";
import { Tabs, TabsList, TabsTrigger, TabsContent } from "@/components/ui/tabs";
import {
  DropdownMenu,
  DropdownMenuTrigger,
  DropdownMenuContent,
  DropdownMenuItem,
} from "@/components/ui/dropdown-menu";
import { Search } from "lucide-react";
import { fetchGoldChart } from "../store/market/goldSlice";
import ChartHigh from "./ChartHigh";

const goldTypes = [
  { value: "sjc", label: "SJC" },
  { value: "pnj", label: "PNJ" },
  { value: "pnj_nhan", label: "PNJ Nhẫn" },
  { value: "vàng_kim_bảo_9999", label: "Kim Bảo 9999" },
  { value: "vàng_phúc_lộc_tài_9999", label: "Phúc Lộc Tài 9999" },
  { value: "pnj_nutrang", label: "PNJ Nữ Trang" },
  { value: "vàng_916_(22k)", label: "Vàng 916 (22K)" },
  { value: "vàng_750_(18k)", label: "Vàng 750 (18K)" },
  { value: "vàng_680_(163k)", label: "Vàng 680 (16.3K)" },
  { value: "vàng_650_(156k)", label: "Vàng 650 (15.6K)" },
  { value: "vàng_610_(146k)", label: "Vàng 610 (14.6K)" },
  { value: "vàng_585_(14k)", label: "Vàng 585 (14K)" },
  { value: "vàng_416_(10k)", label: "Vàng 416 (10K)" },
  { value: "vàng_375_(9k)", label: "Vàng 375 (9K)" },
  { value: "vàng_333_(8k)", label: "Vàng 333 (8K)" },
];

const ranges = [
  { label: "1 Tuần", value: "7d" },
  { label: "1 Tháng", value: "30d" },
  { label: "6 Tháng", value: "6m" },
  { label: "YTD", value: "ytd" },
  { label: "1 Năm", value: "1y" },
  { label: "5 Năm", value: "5y" },
];

function getDaysFromRange(range) {
  const today = new Date();
  switch (range) {
    case "7d":
      return 7;
    case "30d":
      return 30;
    case "6m":
      return 180;
    case "1y":
      return 365;
    case "5y":
      return 365 * 5;
    case "ytd": {
      const startOfYear = new Date(today.getFullYear(), 0, 1);
      const diff = Math.floor((today - startOfYear) / (1000 * 60 * 60 * 24));
      return diff + 1;
    }
    default:
      return 30;
  }
}

export default function GoldChart() {
  const [selectedTypes, setSelectedTypes] = useState(["sjc"]);
  const [range, setRange] = useState("30d");
  const dispatch = useDispatch();
  const { data, loading, error } = useSelector((state) => state.gold);
  const days = getDaysFromRange(range);


  useEffect(() => {
    const days = getDaysFromRange(range);
    const goldToFetch = selectedTypes.filter(
      (type) => !data[type] || !data[type][days]
    );

    if (goldToFetch.length > 0) {
      dispatch(fetchGoldChart({ gold_types: goldToFetch, days }));
    }
  }, [selectedTypes, range, data, dispatch]);

  return (
    <div className="border rounded-md border-[#E7E7E7] shadow p-6">
      <Tabs defaultValue={range} onValueChange={setRange} className="w-full">
        <div className="flex justify-between items-center mb-4">
          <TabsList className="bg-[#FAFAFA] border border-[#E7E7E7] rounded-lg p-0 h-[40px]">
            {ranges.map((tab) => (
              <TabsTrigger
                key={tab.value}
                value={tab.value}
                className="text-sm font-medium px-4 py-2 rounded-md data-[state=active]:bg-white data-[state=active]:text-black text-[#989898] focus:outline-none"
              >
                {tab.label}
              </TabsTrigger>
            ))}
          </TabsList>

          <DropdownMenu>
            <DropdownMenuTrigger asChild>
              <button className="relative flex items-center gap-2 px-4 py-2 border border-[#D5D7DA] rounded-lg shadow text-[#989898] text-sm w-[200px] bg-white">
                <Search className="w-4 h-4" />
                Thêm so sánh
                <svg
                  className="ml-auto w-4 h-4"
                  fill="none"
                  stroke="currentColor"
                  strokeWidth={2}
                  viewBox="0 0 24 24"
                >
                  <path
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    d="M19 9l-7 7-7-7"
                  />
                </svg>
              </button>
            </DropdownMenuTrigger>

            <DropdownMenuContent
              align="end"
              sideOffset={4}
              className="z-50 mt-1 min-w-[200px] rounded-md border border-[#E7E7E7] bg-white shadow"
            >
              {goldTypes.map((item) => (
                <DropdownMenuItem
                  key={item.value}
                  onClick={() => {
                    if (!selectedTypes.includes(item.value)) {
                      setSelectedTypes((prev) => [...prev, item.value]);
                    }
                  }}
                  className="text-sm px-3 py-2 cursor-pointer"
                >
                  {item.label}
                </DropdownMenuItem>
              ))}
            </DropdownMenuContent>
          </DropdownMenu>
        </div>

        <TabsContent value={range}>
          {loading && <p className="text-gray-500">Đang tải dữ liệu...</p>}
          {error && <p className="text-red-500">Lỗi: {error}</p>}
          {!loading &&
            !error &&
            (Object.values(data).some((d) => d?.[days]?.length > 0) ? (
              <ChartHigh
                data={data}
                selectedTypes={selectedTypes}
                setSelectedTypes={setSelectedTypes}
                range={range}
              />
            ) : (
              <p className="text-gray-500">Dữ liệu đang được cập nhật...</p>
            ))}
        </TabsContent>
      </Tabs>
    </div>
  );
}
