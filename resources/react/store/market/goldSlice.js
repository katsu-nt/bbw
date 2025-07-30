import { createSlice, createAsyncThunk } from "@reduxjs/toolkit";

// ─────────────────────────────────────────
// 🎯 /gold/chart
// ─────────────────────────────────────────
export const fetchGoldChart = createAsyncThunk(
  "gold/fetchChart",
  async ({ gold_types = ["sjc"], locations = ["hcm"], days = 7 }, { rejectWithValue }) => {
    try {
      const params = new URLSearchParams();
      gold_types.forEach((type) => params.append("gold_types", type));
      locations.forEach((loc) => params.append("locations", loc));
      params.append("days", days);

      const res = await fetch(`http://127.0.0.1:8003/gold/chart?${params.toString()}`);
      const json = await res.json();

      if (!res.ok || !json.data) {
        return rejectWithValue(json.message || "No data returned");
      }

      return { days, result: json.data };
    } catch (error) {
      return rejectWithValue(error.message);
    }
  }
);

// ─────────────────────────────────────────
// 🎯 /gold/current
// ─────────────────────────────────────────
export const fetchGoldCurrent = createAsyncThunk(
  "gold/fetchCurrent",
  async ({ gold_type, location }, { rejectWithValue }) => {
    try {
      const params = new URLSearchParams({ gold_type, location });
      const res = await fetch(`http://127.0.0.1:8003/gold/current?${params.toString()}`);
      const json = await res.json();

      if (!res.ok || !json.data) {
        return rejectWithValue(json.message || "Không có dữ liệu");
      }

      return {
        key: `${gold_type}-${location}`,
        ...json.data,
      };
    } catch (err) {
      return rejectWithValue(err.message);
    }
  }
);

// ─────────────────────────────────────────
// 🎯 /gold/table
// ─────────────────────────────────────────
export const fetchGoldTable = createAsyncThunk(
  "gold/fetchTable",
  async (selectedDate, { rejectWithValue }) => {
    try {
      const res = await fetch(`http://127.0.0.1:8003/gold/table?selected_date=${selectedDate}`);
      const json = await res.json();

      if (!res.ok || !json.data) {
        return rejectWithValue(json.message || "Không có dữ liệu");
      }

      return json.data;
    } catch (err) {
      return rejectWithValue(err.message);
    }
  }
);

// ─────────────────────────────────────────
// 🔁 Slice gộp chung
// ─────────────────────────────────────────
const goldSlice = createSlice({
  name: "gold",
  initialState: {
    chart: {},       // { [comboKey]: { [days]: [...] } }
    current: {},     // { [comboKey]: { timestamp, sell_price, delta_percent, ... } }
    table: [],

    loading: {
      chart: false,
      current: false,
      table: false,
    },
    error: {
      chart: null,
      current: null,
      table: null,
    },
  },
  reducers: {
    clearGoldData: (state) => {
      state.chart = {};
    },
    clearGoldCurrent: (state) => {
      state.current = {};
    },
    clearGoldTable: (state) => {
      state.table = [];
    },
  },
  extraReducers: (builder) => {
    // ───── chart ─────
    builder
      .addCase(fetchGoldChart.pending, (state) => {
        state.loading.chart = true;
        state.error.chart = null;
      })
      .addCase(fetchGoldChart.fulfilled, (state, action) => {
        state.loading.chart = false;
        const { days, result } = action.payload;
        for (const [comboKey, prices] of Object.entries(result)) {
          if (!state.chart[comboKey]) {
            state.chart[comboKey] = {};
          }
          state.chart[comboKey][days] = prices;
        }
      })
      .addCase(fetchGoldChart.rejected, (state, action) => {
        state.loading.chart = false;
        state.error.chart = action.payload;
      });

    // ───── current ─────
    builder
      .addCase(fetchGoldCurrent.pending, (state) => {
        state.loading.current = true;
        state.error.current = null;
      })
      .addCase(fetchGoldCurrent.fulfilled, (state, action) => {
        state.loading.current = false;
        const { key, ...data } = action.payload;
        state.current[key] = data;
      })
      .addCase(fetchGoldCurrent.rejected, (state, action) => {
        state.loading.current = false;
        state.error.current = action.payload;
      });

    // ───── table ─────
    builder
      .addCase(fetchGoldTable.pending, (state) => {
        state.loading.table = true;
        state.error.table = null;
      })
      .addCase(fetchGoldTable.fulfilled, (state, action) => {
        state.loading.table = false;
        state.table = action.payload;
      })
      .addCase(fetchGoldTable.rejected, (state, action) => {
        state.loading.table = false;
        state.error.table = action.payload;
      });
  },
});

export const {
  clearGoldData,
  clearGoldCurrent,
  clearGoldTable,
} = goldSlice.actions;

export default goldSlice.reducer;
