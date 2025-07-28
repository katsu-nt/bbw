import React from 'react';
import { createRoot } from 'react-dom/client';
import MarketPage from '../pages/MarketPage';
import { store } from "../store"
import { Provider } from "react-redux";

const rootElement  = document.getElementById('react-market');
if (rootElement) {
  const root = createRoot(rootElement)
  root.render(
    <Provider store={store}>
      <MarketPage />
    </Provider>
  )
}
