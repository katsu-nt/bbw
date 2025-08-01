import { Switch } from "@/components/ui/switch";

export default function CompareSwitch({
  checked,
  onChange,
  label = "So sánh",
  id = "compare-switch"
}) {
  return (
    <div className="flex flex-col items-center">
      <Switch
        id={id}
        checked={checked}
        onCheckedChange={onChange}
        className="compare-switch"
      />
      <label
        htmlFor={id}
        className="compare-switch-label"
        style={{
          color: "#595959",
          fontSize: "12px",
          fontWeight: 500,
          letterSpacing: "-1px",
          textAlign: "center",
          marginTop: "0.3rem",
          marginBottom: 0,
        }}
      >
        {label}
      </label>
      <style>{`
        .compare-switch {
          background: #F5F5F5 !important;
          border: none !important;
          box-shadow: 0 2px 8px rgba(110,110,110,0.05);
          width: 38px !important;
          height: 20px !important;
          padding: 2px !important;
          display: inline-flex;
          align-items: center;
          transition: background 0.2s;
        }
        .compare-switch [data-slot="switch-thumb"] {
          background: #fff !important;
          width: 18px !important;
          height: 18px !important;
          box-shadow: 0 2px 4px rgba(110,110,110,0.10);
          transition: transform 0.2s;
        }
      `}</style>
    </div>
  );
}
