import { configureStore } from "@reduxjs/toolkit";

import goldReducer from "./market/goldSlice";
import goldTableReducer from "./market/goldTableSlice";

export const store = configureStore({
  reducer: {
    gold: goldReducer,
    goldTable: goldTableReducer,
  },
});
