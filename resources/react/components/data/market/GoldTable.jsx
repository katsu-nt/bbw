import React, { useState } from "react"
import DatePicker from "@/components/ui/datePicker"

export default function GoldTable() {
  const [selectedDate, setSelectedDate] = useState(new Date())

  return (
    <div className="">
      <DatePicker value={selectedDate} onChange={setSelectedDate} />
    </div>
  )
}
