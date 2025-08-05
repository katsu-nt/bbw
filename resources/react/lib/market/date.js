export function getDaysFromRange(range) {
  const today = new Date();
  switch (range) {
    case "7d": return 7;
    case "30d": return 30;
    case "6m": return 180;
    case "1y": return 365;
    case "5y": return 365 * 5;
    case "ytd": {
      const start = new Date(today.getFullYear(), 0, 1);
      return Math.floor((today - start) / (1000 * 60 * 60 * 24)) + 1;
    }
    default: return 30;
  }
}
