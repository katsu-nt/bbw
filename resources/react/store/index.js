import { configureStore } from "@reduxjs/toolkit";

import goldReducer from "./market/goldSlice";

export const store = configureStore({
  reducer: {
    gold: goldReducer,
  },
});
