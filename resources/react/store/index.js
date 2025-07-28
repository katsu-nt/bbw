import { configureStore } from "@reduxjs/toolkit";

import goldReducer from "./market/goldSlice";
// import exchangeReducer from "./market/exchangeSlice"; 

export const store = configureStore({
  reducer: {
    gold: goldReducer,
  },
});
