import { Sun, Moon } from "lucide-react";
import "../../theme/css/theme-toggle.css";

function ThemeToggle({ isDark, onToggle, size = "default" }) {
    return (
        <button
            className={`theme-toggle ${size}`}
            onClick={onToggle}
            title={isDark ? "Switch to light mode" : "Switch to dark mode"}
        >
            {isDark ? <Sun size={size === "small" ? 16 : 20} /> : <Moon size={size === "small" ? 16 : 20} />}
        </button>
    );
}

export default ThemeToggle;
