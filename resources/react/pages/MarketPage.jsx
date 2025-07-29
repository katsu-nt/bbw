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
import { Tabs, TabsList, TabsTrigger, TabsContent } from "@/components/ui/tabs"
import GoldContent from "../components/GoldContent";

export default function MarketPage() {
  return (
    <DataLayout>
      <div className="border-t border-b py-10 px-8 border-solid border-Line_02">
        <Breadcrumb>
          <BreadcrumbList>
            <BreadcrumbItem>
              <BreadcrumbLink className="text-lg font-bold" href="/du-lieu/doanh-nghiep">
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
          <TabsList>
            <TabsTrigger value="gold">Giá vàng</TabsTrigger>
            <TabsTrigger value="exchange">Tỷ giá</TabsTrigger>
          </TabsList>

          <TabsContent value="gold">
            <GoldContent/>
          </TabsContent>

          <TabsContent value="exchange">
            <div className="mt-4 p-4 bg-gray-100 rounded-md">
              <h2 className="text-lg font-bold">Tỷ giá ngoại tệ</h2>
              <p>Hiển thị bảng tỷ giá tại đây...</p>
            </div>
          </TabsContent>
        </Tabs>
      </div>
    </DataLayout>
  );
}
