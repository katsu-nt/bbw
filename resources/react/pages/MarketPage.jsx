import React from "react";
import DataLayout from "../layouts/DataLayout";
import { Breadcrumb } from "@/components/ui/breadcrumb";
import {
  BreadcrumbItem,
  BreadcrumbLink,
  BreadcrumbList,
  BreadcrumbPage,
  BreadcrumbSeparator,
} from "@/components/ui/breadcrumb";
import { Tabs, TabsList, TabsTrigger, TabsContent } from "@/components/ui/tabs";
import GoldContent from "../components/data/market/GoldContent";
import ExchangeContent from "../components/data/market/ExchangeContent";

export default function MarketPage() {
  return (
    <DataLayout>
      <div className="border-t border-b py-10 px-8 border-solid border-Line_02">
        <Breadcrumb>
          <BreadcrumbList>
            <BreadcrumbItem>
              <BreadcrumbLink
                className="text-lg font-bold"
                href="/du-lieu/doanh-nghiep"
              >
                Dữ liệu
              </BreadcrumbLink>
            </BreadcrumbItem>
            <BreadcrumbSeparator
              verticalAlign="middle"
              color="#323232"
              height="26px"
              width="1px"
              opacity={1}
            />
            <BreadcrumbItem>
              <BreadcrumbLink
                className="text-lg font-bold"
                href="/du-lieu/thi-truong"
              >
                Thị trường
              </BreadcrumbLink>
            </BreadcrumbItem>
          </BreadcrumbList>
        </Breadcrumb>
        <Tabs defaultValue="gold" className="w-full">
          <TabsList className="inline-flex h-[36px] bg-[#FAFAFA] rounded-lg shadow-[inset_0_0_0_1px_#E7E7E7] overflow-hidden mt-4 mb-2">
            <TabsTrigger
              value="gold"
              className="!text-[16px] font-semibold h-full px-2 py-2 text-[#989898] border border-transparent focus:outline-none 
      data-[state=active]:text-black 
      data-[state=active]:bg-white 
      data-[state=active]:border-[#D5D7DA] 
      data-[state=active]:z-10 
      first:data-[state=active]:rounded-l-lg 
      last:data-[state=active]:rounded-r-lg"
            >
              Giá vàng
            </TabsTrigger>
            <TabsTrigger
              value="exchange"
              className="!text-[16px] font-semibold h-full px-2 py-2 text-[#989898] border border-transparent focus:outline-none 
      data-[state=active]:text-black 
      data-[state=active]:bg-white 
      data-[state=active]:border-[#D5D7DA] 
      data-[state=active]:z-10 
      first:data-[state=active]:rounded-l-lg 
      last:data-[state=active]:rounded-r-lg"
            >
              Tỷ giá
            </TabsTrigger>
          </TabsList>
          <TabsContent value="gold">
            <GoldContent />
          </TabsContent>

          <TabsContent value="exchange">
            <ExchangeContent />
          </TabsContent>
        </Tabs>
      </div>
    </DataLayout>
  );
}
