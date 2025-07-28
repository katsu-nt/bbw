import { createSlice, createAsyncThunk } from "@reduxjs/toolkit";

// Async thunk
export const fetchGoldChart = createAsyncThunk(
  "gold/fetchChart",
  async ({ gold_types = ["sjc"], days = 30 }, { rejectWithValue }) => {
    try {
      const params = new URLSearchParams();
      gold_types.forEach((type) => params.append("gold_types", type));
      params.append("days", days);

      const res = await fetch(
        `https://market-chart-v2.onrender.com/gold/chart?${params.toString()}`
      );

      const json = await res.json();

      if (!res.ok || !json.data) {
        return rejectWithValue(json.message || "No data returned");
      }

      // Trả về days và data dạng { sjc: [...], pnj: [...] }
      return { days, result: json.data };
    } catch (error) {
      return rejectWithValue(error.message);
    }
  }
);

// Slice
const goldSlice = createSlice({
  name: "gold",
  initialState: {
    data: {}, // dạng: { sjc: { 30: [...] }, pnj: { 30: [...] } }
    loading: false,
    error: null,
  },
  reducers: {
    clearGoldData: (state) => {
      state.data = {};
    },
  },
  extraReducers: (builder) => {
    builder
      .addCase(fetchGoldChart.pending, (state) => {
        state.loading = true;
        state.error = null;
      })
      .addCase(fetchGoldChart.fulfilled, (state, action) => {
        state.loading = false;

        const { days, result } = action.payload;

        for (const [goldType, prices] of Object.entries(result)) {
          if (!state.data[goldType]) {
            state.data[goldType] = {};
          }
          state.data[goldType][days] = prices;
        }
      })
      .addCase(fetchGoldChart.rejected, (state, action) => {
        state.loading = false;
        state.error = action.payload || "Đã có lỗi xảy ra";
      });
  },
});

export const { clearGoldData } = goldSlice.actions;
export default goldSlice.reducer;
