import {
  DropdownMenu,
  DropdownMenuTrigger,
  DropdownMenuContent,
  DropdownMenuItem,
} from "@/components/ui/dropdown-menu";
import { Search, ChevronDown } from "lucide-react";

export default function AddItemDropdown({
  options,
  selectedItems,
  getKey,
  onSelect,
  buttonLabel = "Thêm mục so sánh",
  emptyText = "Không còn mục nào để chọn",
  renderLabel, // optional, custom label render
}) {
  // Filter options đã chọn
  const filteredOptions = options.filter(
    (item) => !selectedItems.some((s) => getKey(s) === getKey(item))
  );
  return (
    <DropdownMenu>
      <DropdownMenuTrigger asChild>
        <button className="relative flex items-center justify-between gap-2 px-4 py-2 border border-[#D5D7DA] rounded-lg shadow text-[#595959] text-base w-[220px] bg-white">
          <div className="flex items-center gap-2 text-base">
            <Search className="w-4 h-4 text-[#A4A7AE]" />
            {buttonLabel}
          </div>
          <ChevronDown className="w-4 h-4 text-[#BBBBBB]" />
        </button>
      </DropdownMenuTrigger>
      <DropdownMenuContent
        align="end"
        sideOffset={4}
        className="z-50 mt-1 max-h-[300px] overflow-y-auto w-[--radix-popper-anchor-width] rounded-md border border-[#E7E7E7] bg-white shadow"
      >
        {filteredOptions.length === 0 ? (
          <DropdownMenuItem disabled>{emptyText}</DropdownMenuItem>
        ) : (
          filteredOptions.map((item) => (
            <DropdownMenuItem
              key={getKey(item)}
              onClick={() => onSelect(item)}
              className="text-sm px-3 py-2 cursor-pointer"
            >
              {renderLabel ? renderLabel(item) : item.name}
            </DropdownMenuItem>
          ))
        )}
      </DropdownMenuContent>
    </DropdownMenu>
  );
}
