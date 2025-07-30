import { createSlice, createAsyncThunk } from "@reduxjs/toolkit";

// ✅ API call với gold_types và locations tách riêng
export const fetchGoldChart = createAsyncThunk(
  "gold/fetchChart",
  async ({ gold_types = ["sjc"], locations = ["hcm"], days = 7 }, { rejectWithValue }) => {
    try {
      const params = new URLSearchParams();
      gold_types.forEach((type) => params.append("gold_types", type));
      locations.forEach((loc) => params.append("locations", loc));
      params.append("days", days);

      const res = await fetch(
        `http://127.0.0.1:8003/gold/chart?${params.toString()}`
      );

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

const goldSlice = createSlice({
  name: "gold",
  initialState: {
    data: {}, // key = `${gold_type}-${location}`
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

        for (const [comboKey, prices] of Object.entries(result)) {
          if (!state.data[comboKey]) {
            state.data[comboKey] = {};
          }
          state.data[comboKey][days] = prices;
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
