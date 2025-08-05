import MultiLineChart from "@/components/data/market/shared/MultiLineChart";
import {
  exchangeSeriesAdapter,
  exchangeYAxisAdapter,
  exchangeTooltipFormatter,
  exchangeLegendItem,
  exchangeUnitSideText,
  exchangeColors,
} from "@/charts/adapter/exchangeChartAdapter";

export default function ExchangeChart(props) {
  return (
    <MultiLineChart
      items={props.chartItems}
      setItems={props.setChartItems}
      data={props.data}
      range={props.range}
      mode={props.mode}
      options={props.options}
      seriesAdapter={exchangeSeriesAdapter}
      yAxesAdapter={exchangeYAxisAdapter}
      tooltipFormatter={exchangeTooltipFormatter}
      chartColors={exchangeColors}
      unitSideText={exchangeUnitSideText(props.chartItems, props.mode)}
      renderLegendItem={(args) =>
        exchangeLegendItem({ ...args, items: props.chartItems, setItems: props.setChartItems, options: props.options })
      }
    />
  );
}
