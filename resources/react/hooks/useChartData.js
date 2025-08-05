import { useEffect } from "react";

export function useChartData({
  items,
  data,
  days,
  dispatch,
  fetchAction,
  getKey,     // function lấy key unique cho từng item
  getParams,  // function trả về params cho fetchAction
}) {
  useEffect(() => {
    const needFetch = items.filter((item) => {
      const key = getKey(item);
      return !data[key] || !data[key][days];
    });
    if (needFetch.length > 0) {
      dispatch(fetchAction(getParams(needFetch, days)));
    }
  }, [items, data, days, dispatch, fetchAction, getKey, getParams]);
}
