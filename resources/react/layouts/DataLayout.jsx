import React from "react";

export default function MarketLayout({ children }) {
  return (
    <div className="mx-auto py-10 border border-solid border-Line_02" style={{ maxWidth: "1362px" }}>
      {children}
    </div>
  );
}
