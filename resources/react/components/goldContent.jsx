import GoldChart from "./GoldChart";
export default function GoldContent() {
  return (
    <>
      <div className="flex flex-col">
        <div className="flex gap-4">
          <div className="flex flex-col">
            <div className="text-sm font-semibold">Giá vàng thế giới</div>
            <div>XAU/USD 107,500,000 0.51%</div>
          </div>

          <div className="w-px bg-[#323232] opacity-100" />

          <div className="flex flex-col">
            <div className="text-sm font-semibold">Giá vàng trong nước</div>
            <div>XAU/USD 107,500,000 0.51%</div>
          </div>
        </div>
        <div className="flex flex-col">
          <div>14 tháng 7,2025</div>
          <div>Đơn vị: nghìn đồng/lượng</div>
        </div>
      </div>
      <div className="flex w-full">
        <div className="basis-2/3 p-2 pl-0"><GoldChart/></div>
        <div className="basis-1/3 p-2">Tab</div>
      </div>
    </>
  );
}
