import { createSlice, createAsyncThunk } from "@reduxjs/toolkit";
const EXCHANGE_URL = "https://market-chart-v2.onrender.com/api/v1/exchange";
//const EXCHANGE_URL = "http://localhost:8003/api/v1/exchange";
// GET /exchange/chart
export const fetchExchangeChart = createAsyncThunk(
  "exchange/fetchChart",
  async (
    { type = "market", code = ["USD"], days = 30 },
    { rejectWithValue }
  ) => {
    try {
      const params = new URLSearchParams();
      params.append("type", type);
      (Array.isArray(code) ? code : [code]).forEach((c) =>
        params.append("code", c)
      );
      params.append("days", days);

      const res = await fetch(`${EXCHANGE_URL}/chart?${params.toString()}`);
      const json = await res.json();
      if (!res.ok || !json.data)
        return rejectWithValue(json.message || "No data");
      return {
        type,
        code: Array.isArray(code) ? code : [code],
        days,
        result: json.data,
      };
    } catch (err) {
      return rejectWithValue(err.message);
    }
  }
);

// GET /exchange/latest
export const fetchExchangeCurrent = createAsyncThunk(
  "exchange/fetchCurrent",
  async (
    { type = "market", code = "USD" },
    { rejectWithValue }
  ) => {
    try {
      const params = new URLSearchParams({ type, code });
      const res = await fetch(`${EXCHANGE_URL}/latest?${params.toString()}`);
      const json = await res.json();
      if (!res.ok || !json.data)
        return rejectWithValue(json.message || "No data");
      // Chỉ trả về trường data để state.current[key] = { rate, delta_percent, ... }
      return {
        key: `${type}-${code}`,
        data: json.data,
      };
    } catch (err) {
      return rejectWithValue(err.message);
    }
  }
);

// GET /exchange/table
export const fetchExchangeTable = createAsyncThunk(
  "exchange/fetchTable",
  async ({ type = "market", date, code }, { rejectWithValue }) => {
    try {
      const params = new URLSearchParams();
      params.append("type", type);
      if (code) params.append("code", code);
      if (date) params.append("date", date);

      const res = await fetch(`${EXCHANGE_URL}/table?${params.toString()}`);
      const json = await res.json();
      if (!res.ok || !json.data)
        return rejectWithValue(json.message || "No data");
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
    clearExchangeChart: (state) => {
      state.chart = {};
    },
    clearExchangeCurrent: (state) => {
      state.current = {};
    },
    clearExchangeTable: (state) => {
      state.table = [];
    },
  },
  extraReducers: (builder) => {
    builder
      // chart
      .addCase(fetchExchangeChart.pending, (state) => {
        state.loading.chart = true;
        state.error.chart = null;
      })
      .addCase(fetchExchangeChart.fulfilled, (state, action) => {
        state.loading.chart = false;
        const { type, code, days, result } = action.payload;
        for (const codeKey of code) {
          if (!state.chart[`${type}-${codeKey}`])
            state.chart[`${type}-${codeKey}`] = {};
          state.chart[`${type}-${codeKey}`][days] = result[codeKey];
        }
      })
      .addCase(fetchExchangeChart.rejected, (state, action) => {
        state.loading.chart = false;
        state.error.chart = action.payload;
      })

      // current
      .addCase(fetchExchangeCurrent.pending, (state) => {
        state.loading.current = true;
        state.error.current = null;
      })
      .addCase(fetchExchangeCurrent.fulfilled, (state, action) => {
        state.loading.current = false;
        const { key, data } = action.payload;
        state.current[key] = data;
      })
      .addCase(fetchExchangeCurrent.rejected, (state, action) => {
        state.loading.current = false;
        state.error.current = action.payload;
      })

      // table
      .addCase(fetchExchangeTable.pending, (state) => {
        state.loading.table = true;
        state.error.table = null;
      })
      .addCase(fetchExchangeTable.fulfilled, (state, action) => {
        state.loading.table = false;
        state.table = action.payload;
      })
      .addCase(fetchExchangeTable.rejected, (state, action) => {
        state.loading.table = false;
        state.error.table = action.payload;
      });
  },
});

export const { clearExchangeChart, clearExchangeCurrent, clearExchangeTable } =
  exchangeSlice.actions;

export default exchangeSlice.reducer;
