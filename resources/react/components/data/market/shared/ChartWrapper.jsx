import ChartSkeleton from "./ChartSkeleton";

export default function ChartWrapper({ loading, error, loadingText, errorText, children }) {
  if (loading || error) {
    return <ChartSkeleton loading={loading} error={error} loadingText={loadingText} errorText={errorText} />;
  }
  return <>{children}</>;
}
