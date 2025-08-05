// utils/format.js
export const formatVietnamDateTime = (isoString) => {
  if (!isoString) return "";
  const date = new Date(isoString);
  return date.toLocaleString("vi-VN", {
    hour12: false,
    year: "numeric",
    month: "2-digit",
    day: "2-digit",
    hour: "2-digit",
    minute: "2-digit",
    second: "2-digit",
    timeZone: "Asia/Ho_Chi_Minh",
  });
};

export const formatPriceVND = (price) =>
  typeof price === "number"
    ? price.toLocaleString("vi-VN") + " VND"
    : "";

export const formatPriceUSD = (price) =>
  typeof price === "number"
    ? price.toLocaleString("en-US") + " USD"
    : "";

export const formatPercent = (delta) =>
  typeof delta === "number"
    ? `${delta > 0 ? "+" : ""}${delta.toFixed(2)}%`
    : "";
