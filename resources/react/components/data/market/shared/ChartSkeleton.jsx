import { ResponseStatus } from "@/components/ui/responseStatus";

export default function ChartSkeleton({ loading, error, loadingText, errorText }) {
  return (
    <div className="w-full space-y-6">
      <div className="flex flex-wrap gap-3 items-center">
        {[...Array(3)].map((_, i) => (
          <div key={i} className="h-8 w-32 rounded-full bg-gray-200" />
        ))}
      </div>
      <div className="relative w-full h-[400px] rounded-md bg-gray-100 border border-gray-200 flex items-center justify-center">
        {loading ? (
          <ResponseStatus status="loading" message={loadingText || "Đang tải dữ liệu..."} />
        ) : (
          <ResponseStatus status="error" message={errorText || "Không thể kết nối đến máy chủ"} />
        )}
      </div>
    </div>
  );
}
