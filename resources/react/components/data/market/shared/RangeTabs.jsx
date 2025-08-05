import { TabsList, TabsTrigger } from "@/components/ui/tabs";

export default function RangeTabs({ ranges, value, onChange }) {
  return (
    <TabsList className="inline-flex h-[40px] bg-[#FAFAFA] rounded-lg shadow-[inset_0_0_0_1px_#E7E7E7] overflow-hidden">
      {ranges.map((tab) => (
        <TabsTrigger
          key={tab.value}
          value={tab.value}
          onClick={() => onChange(tab.value)}
          className={`text-sm font-semibold h-full px-4 py-2 text-[#989898] border border-transparent focus:outline-none data-[state=active]:text-black data-[state=active]:bg-white  data-[state=active]:border-[#D5D7DA] data-[state=active]:z-10 first:data-[state=active]:rounded-l-lg last:data-[state=active]:rounded-r-lg`}
        >
          {tab.label}
        </TabsTrigger>
      ))}
    </TabsList>
  );
}
