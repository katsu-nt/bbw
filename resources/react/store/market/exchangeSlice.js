import { createSlice, createAsyncThunk } from "@reduxjs/toolkit";
const EXCHANGE_URL = "http://localhost:8003/api/exchange";

// GET /exchange/chart
export const fetchExchangeChart = createAsyncThunk(
  "exchange/fetchChart",
  async ({ base_currencies = ["USD"], quote_currencies = ["VND"], days = 30 }, { rejectWithValue }) => {
    try {
      const params = new URLSearchParams();
      base_currencies.forEach((code) => params.append("base_currencies", code));
      quote_currencies.forEach((code) => params.append("quote_currencies", code));
      params.append("days", days);

      const res = await fetch(`${EXCHANGE_URL}/chart?${params.toString()}`);
      const json = await res.json();
      if (!res.ok || !json.data) return rejectWithValue(json.message || "No data");
      return { days, result: json.data };
    } catch (err) {
      return rejectWithValue(err.message);
    }
  }
);

// GET /exchange/current
export const fetchExchangeCurrent = createAsyncThunk(
  "exchange/fetchCurrent",
  async ({ base_currency, quote_currency }, { rejectWithValue }) => {
    try {
      const params = new URLSearchParams({ base_currency, quote_currency });
      const res = await fetch(`${EXCHANGE_URL}/current?${params.toString()}`);
      const json = await res.json();
      if (!res.ok || !json.data) return rejectWithValue(json.message || "No data");
      return {
        key: `${base_currency}-${quote_currency}`,
        ...json.data,
      };
    } catch (err) {
      return rejectWithValue(err.message);
    }
  }
);

// GET /exchange/table
export const fetchExchangeTable = createAsyncThunk(
  "exchange/fetchTable",
  async (selectedDate, { rejectWithValue }) => {
    try {
      const res = await fetch(`${EXCHANGE_URL}/table?selected_date=${selectedDate}`);
      const json = await res.json();
      if (!res.ok || !json.data) return rejectWithValue(json.message || "No data");
      return json.data;
    } catch (err) {
      return rejectWithValue(err.message);
    }
  }
);

const exchangeSlice = createSlice({
  name: "exchange",
  initialState: {
    chart: {},
    current: {},
    table: [],
    loading: { chart: false, current: false, table: false },
    error: { chart: null, current: null, table: null },
  },
  reducers: {
    clearExchangeChart: (state) => { state.chart = {}; },
    clearExchangeCurrent: (state) => { state.current = {}; },
    clearExchangeTable: (state) => { state.table = []; },
  },
  extraReducers: (builder) => {
    builder
      // chart
      .addCase(fetchExchangeChart.pending, (state) => {
        state.loading.chart = true; state.error.chart = null;
      })
      .addCase(fetchExchangeChart.fulfilled, (state, action) => {
        state.loading.chart = false;
        const { days, result } = action.payload;
        for (const [comboKey, rates] of Object.entries(result)) {
          if (!state.chart[comboKey]) state.chart[comboKey] = {};
          state.chart[comboKey][days] = rates;
        }
      })
      .addCase(fetchExchangeChart.rejected, (state, action) => {
        state.loading.chart = false; state.error.chart = action.payload;
      })

      // current
      .addCase(fetchExchangeCurrent.pending, (state) => {
        state.loading.current = true; state.error.current = null;
      })
      .addCase(fetchExchangeCurrent.fulfilled, (state, action) => {
        state.loading.current = false;
        const { key, ...data } = action.payload;
        state.current[key] = data;
      })
      .addCase(fetchExchangeCurrent.rejected, (state, action) => {
        state.loading.current = false; state.error.current = action.payload;
      })

      // table
      .addCase(fetchExchangeTable.pending, (state) => {
        state.loading.table = true; state.error.table = null;
      })
      .addCase(fetchExchangeTable.fulfilled, (state, action) => {
        state.loading.table = false;
        state.table = action.payload;
      })
      .addCase(fetchExchangeTable.rejected, (state, action) => {
        state.loading.table = false; state.error.table = action.payload;
      });
  },
});

export const {
  clearExchangeChart,
  clearExchangeCurrent,
  clearExchangeTable,
} = exchangeSlice.actions;

export default exchangeSlice.reducer;
