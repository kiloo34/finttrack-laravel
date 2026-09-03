import * as React from "react"
import { Check, ChevronsUpDown } from "lucide-react"

import { cn } from "@/lib/utils"
import { Button } from "@/components/ui/button"
import {
  Command,
  CommandEmpty,
  CommandGroup,
  CommandInput,
  CommandItem,
  CommandList,
} from "@/components/ui/command"
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from "@/components/ui/popover"

export interface ComboboxOption {
  label: string
  value: string
  group?: string
}

interface ComboboxProps {
  options: ComboboxOption[]
  value: string
  onChange: (value: string) => void
  placeholder?: string
  emptyText?: string
  disabled?: boolean
}

const HighlightedText = ({ text, highlight }: { text: string; highlight: string }) => {
  if (!highlight.trim()) {
    return <span>{text}</span>
  }
  
  // Escape regex special characters
  const escapeRegExp = (string: string) => string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
  const regex = new RegExp(`(${escapeRegExp(highlight)})`, "gi")
  const parts = text.split(regex)

  return (
    <span>
      {parts.map((part, i) =>
        regex.test(part) ? (
          <span key={i} className="bg-primary/20 text-primary font-semibold">
            {part}
          </span>
        ) : (
          <span key={i}>{part}</span>
        )
      )}
    </span>
  )
}

export function Combobox({
  options = [],
  value,
  onChange,
  placeholder = "Select an option...",
  emptyText = "No option found.",
  disabled = false,
}: ComboboxProps) {
  const [open, setOpen] = React.useState(false)
  const [searchQuery, setSearchQuery] = React.useState("")
  
  const safeOptions = Array.isArray(options) ? options : [];
  
  // Group options
  const groupedOptions = React.useMemo(() => {
    const groups: Record<string, ComboboxOption[]> = {
      '': [] // Ungrouped options
    };
    
    safeOptions.forEach(option => {
      const groupName = option.group || '';
      if (!groups[groupName]) {
        groups[groupName] = [];
      }
      groups[groupName].push(option);
    });
    
    return groups;
  }, [safeOptions]);

  return (
    <Popover modal={true} open={open} onOpenChange={(isOpen) => {
      setOpen(isOpen)
      if (!isOpen) setSearchQuery("") // Clear search when closed
    }}>
      <PopoverTrigger asChild>
        <Button
          variant="outline"
          role="combobox"
          aria-expanded={open}
          className="w-full justify-between font-normal"
          disabled={disabled}
        >
          {value
            ? safeOptions.find((option) => option.value === value)?.label
            : placeholder}
          <ChevronsUpDown className="ml-2 h-4 w-4 shrink-0 opacity-50" />
        </Button>
      </PopoverTrigger>
      <PopoverContent className="w-[--radix-popover-trigger-width] p-0" align="start">
        <Command>
          <CommandInput 
            placeholder={placeholder} 
            value={searchQuery} 
            onValueChange={setSearchQuery} 
          />
          <CommandList>
            <CommandEmpty>{emptyText}</CommandEmpty>
            
            {/* Render ungrouped options first */}
            {groupedOptions[''].length > 0 && (
              <CommandGroup>
                {groupedOptions[''].map((option) => (
                  <CommandItem
                    key={option.value}
                    value={option.label}
                    onSelect={() => {
                      onChange(option.value === value ? "" : option.value)
                      setOpen(false)
                      setSearchQuery("")
                    }}
                  >
                    <Check
                      className={cn(
                        "mr-2 h-4 w-4",
                        value === option.value ? "opacity-100" : "opacity-0"
                      )}
                    />
                    <HighlightedText text={option.label} highlight={searchQuery} />
                  </CommandItem>
                ))}
              </CommandGroup>
            )}

            {/* Render grouped options */}
            {Object.entries(groupedOptions).filter(([key]) => key !== '').map(([groupName, items]) => (
              <CommandGroup key={groupName} heading={groupName}>
                {items.map((option) => (
                  <CommandItem
                    key={option.value}
                    value={option.label}
                    onSelect={() => {
                      onChange(option.value === value ? "" : option.value)
                      setOpen(false)
                      setSearchQuery("")
                    }}
                  >
                    <Check
                      className={cn(
                        "mr-2 h-4 w-4",
                        value === option.value ? "opacity-100" : "opacity-0"
                      )}
                    />
                    <HighlightedText text={option.label} highlight={searchQuery} />
                  </CommandItem>
                ))}
              </CommandGroup>
            ))}
          </CommandList>
        </Command>
      </PopoverContent>
    </Popover>
  )
}
