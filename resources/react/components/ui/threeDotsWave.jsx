export default function ThreeDotsWave({ size = "16px", color = "#A0A0A0" }) {
  return (
    <div className="flex justify-end gap-[3px] pr-1">
      <span className="dot dot1">●</span>
      <span className="dot dot2">●</span>
      <span className="dot dot3">●</span>

      <style>{`
        .dot {
          font-size: ${size};
          color: ${color};
          animation: pulse 1.2s infinite ease-in-out;
          display: inline-block;
          transform-origin: center;
        }
        .dot1 {
          animation-delay: 0s;
        }
        .dot2 {
          animation-delay: 0.2s;
        }
        .dot3 {
          animation-delay: 0.4s;
        }

        @keyframes pulse {
          0%, 80%, 100% {
            transform: scale(1);
            opacity: 0.8;
          }
          40% {
            transform: scale(1.5);
            opacity: 1;
          }
        }
      `}</style>
    </div>
  );
}
