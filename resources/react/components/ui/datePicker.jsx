import React, { useState, useRef, useEffect } from "react";
import dayjs from "dayjs";
import "dayjs/locale/vi";
import { ChevronLeft, ChevronRight, ChevronDown } from "lucide-react";
dayjs.locale("vi");

const now = dayjs();
const yearsPerPage = 12;

export default function DatePicker() {
  const [showPicker, setShowPicker] = useState(false);
  const [mode, setMode] = useState("day");
  const [selectedDate, setSelectedDate] = useState(now);
  const [viewDate, setViewDate] = useState(now);
  const pickerRef = useRef(null);
  useEffect(() => {
    const handleClickOutside = (event) => {
      if (pickerRef.current && !pickerRef.current.contains(event.target)) {
        setShowPicker(false);
      }
    };
    if (showPicker) {
      document.addEventListener("mousedown", handleClickOutside);
    } else {
      document.removeEventListener("mousedown", handleClickOutside);
    }
    return () => {
      document.removeEventListener("mousedown", handleClickOutside);
    };
  }, [showPicker]);
  const handleSelectDate = (date) => {
    setSelectedDate(date);
    setViewDate(date);
    setShowPicker(false);
    setMode("day");
  };

  const renderHeader = () => (
    <div className="flex justify-between items-center p-4 text-[#414651]">
      <button
        onClick={handlePrev}
        className="text-[#A4A7AE] hover:text-black transition-colors"
      >
        <ChevronLeft size={18} />
      </button>
      <div className="flex gap-2 text-sm font-medium">
        <button onClick={() => setMode("month")}>
          Tháng {viewDate.month() + 1}
        </button>
        <button onClick={() => setMode("year")}>{viewDate.year()}</button>
      </div>
      <button
        onClick={handleNext}
        className="text-[#A4A7AE] hover:text-black transition-colors"
      >
        <ChevronRight size={18} />
      </button>
    </div>
  );

  const handlePrev = () => {
    if (mode === "year") {
      setViewDate(viewDate.subtract(yearsPerPage, "year"));
    } else if (mode === "month") {
      setViewDate(viewDate.subtract(1, "year"));
    } else {
      setViewDate(viewDate.subtract(1, "month"));
    }
  };

  const handleNext = () => {
    if (mode === "year") {
      setViewDate(viewDate.add(yearsPerPage, "year"));
    } else if (mode === "month") {
      setViewDate(viewDate.add(1, "year"));
    } else {
      setViewDate(viewDate.add(1, "month"));
    }
  };

  const renderDays = () => {
    const startOfMonth = viewDate.startOf("month");
    const startDay = (startOfMonth.day() + 6) % 7;
    const daysInMonth = viewDate.daysInMonth();

    const prevMonth = viewDate.subtract(1, "month");
    const prevDays = prevMonth.daysInMonth();

    const totalCells = 42;
    const days = [];

    for (let i = 0; i < totalCells; i++) {
      let day,
        currentMonth = true;
      if (i < startDay) {
        day = prevMonth.date(prevDays - (startDay - i) + 1);
        currentMonth = false;
      } else if (i < startDay + daysInMonth) {
        day = viewDate.date(i - startDay + 1);
      } else {
        day = viewDate.add(1, "month").date(i - startDay - daysInMonth + 1);
        currentMonth = false;
      }

      days.push(
        <button
          key={i}
          disabled={!currentMonth}
          onClick={() => handleSelectDate(day)}
          className={`w-8 h-8 flex items-center justify-center text-sm text rounded-full transition-colors duration-150
  ${currentMonth ? "" : "cursor-default"}
  ${
    day.isSame(selectedDate, "date")
      ? "bg-[#6E6E6E] text-white"
      : currentMonth
      ? "text-[#414651] hover:bg-gray-200"
      : "text-[#717680]"
  }`}
        >
          {day.date()}
        </button>
      );
    }

    return (
      <div className="grid grid-cols-7 gap-1 px-4 pb-4">
        {["CN", "T2", "T3", "T4", "T5", "T6", "T7"].map((d) => (
          <div
            key={d}
            className="w-8 h-8 flex items-center justify-center text-xs font-medium text-[#414651]"
          >
            {d}
          </div>
        ))}
        {days}
      </div>
    );
  };

  const renderMonths = () => (
    <div className="grid grid-cols-4 gap-2 p-4">
      {Array.from({ length: 12 }, (_, i) => (
        <button
          key={i}
          onClick={() => {
            setViewDate(viewDate.month(i));
            setMode("day");
          }}
          className={`text-sm px-2 py-4 rounded transition-colors duration-150
            ${
              i === selectedDate.month() &&
              viewDate.year() === selectedDate.year()
                ? "bg-[#6E6E6E] text-white"
                : "text-[#414651] hover:bg-gray-200 hover:text-[#414651]"
            }`}
        >
          {`Tháng ${i + 1}`}
        </button>
      ))}
    </div>
  );

  const renderYears = () => {
    const currentYear = viewDate.year();
    const startYear = Math.floor(currentYear / yearsPerPage) * yearsPerPage;

    return (
      <div className="grid grid-cols-3 gap-2 p-4">
        {Array.from({ length: yearsPerPage }, (_, i) => {
          const year = startYear + i;
          return (
            <button
              key={year}
              onClick={() => {
                setViewDate(viewDate.year(year));
                setMode("month");
              }}
              className={`text-sm px-2 py-1 rounded transition-colors duration-150
                ${
                  year === selectedDate.year()
                    ? "bg-[#6E6E6E] text-white"
                    : "text-[#414651] hover:bg-gray-200 hover:text-[#414651]"
                }`}
            >
              {year}
            </button>
          );
        })}
      </div>
    );
  };

  return (
    <div ref={pickerRef} className="relative w-full">
      <button
        onClick={() => setShowPicker(!showPicker)}
        className="border rounded-md border-[#D5D7DA] shadow px-4 py-2 text-sm text-left flex justify-between w-full text-[#595959]"
      >
        <div>{selectedDate.format("DD/MM/YYYY")}</div>
        <div>
          <ChevronDown className="text-[#595959]" />
        </div>
      </button>

      {showPicker && (
        <div className="absolute z-50 mt-2 bg-white shadow-xs min-w-72 w-full border rounded-md border-[#D5D7DA] shadowborder rounded-md border-[#D5D7DA] shadow">
          {renderHeader()}
          {mode === "year" && renderYears()}
          {mode === "month" && renderMonths()}
          {mode === "day" && renderDays()}
        </div>
      )}
    </div>
  );
}
