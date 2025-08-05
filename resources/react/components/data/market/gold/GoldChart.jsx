import MultiLineChart from "@/components/data/market/shared/MultiLineChart";
import {
  goldSeriesAdapter,
  goldYAxisAdapter,
  goldTooltipFormatter,
  goldLegendItem,
  goldUnitSideText,
  goldColors,
} from "@/charts/adapter/goldChartAdapter";

export default function GoldChart(props) {
  return (
    <MultiLineChart
      items={props.goldItems}
      setItems={props.setGoldItems}
      data={props.data}
      range={props.range}
      mode={props.mode}
      options={props.options}
      seriesAdapter={goldSeriesAdapter}
      yAxesAdapter={goldYAxisAdapter}
      tooltipFormatter={goldTooltipFormatter}    // <-- Đảm bảo truyền vào!
      chartColors={goldColors}
      unitSideText={goldUnitSideText(props.goldItems, props.mode)}
      renderLegendItem={(args) => goldLegendItem({ ...args, items: props.goldItems, setItems: props.setGoldItems })}
    />
  );
}
