import { createSlice, createAsyncThunk } from "@reduxjs/toolkit";

export const fetchGoldTable = createAsyncThunk(
  "goldTable/fetch",
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

const goldTableSlice = createSlice({
  name: "goldTable",
  initialState: {
    data: [],
    loading: false,
    error: null,
  },
  reducers: {},
  extraReducers: (builder) => {
    builder
      .addCase(fetchGoldTable.pending, (state) => {
        state.loading = true;
        state.error = null;
      })
      .addCase(fetchGoldTable.fulfilled, (state, action) => {
        state.loading = false;
        state.data = action.payload;
      })
      .addCase(fetchGoldTable.rejected, (state, action) => {
        state.loading = false;
        state.error = action.payload || "Lỗi không xác định";
      });
  },
});

export default goldTableSlice.reducer;
