import "../../theme/css/auth-grid.css";

import { useEffect, useState } from "react";

const RAYS = 6;

function AuthBackground() {
    const [count, setCount] = useState(256);

    useEffect(() => {
        const update = () => {
            const tile = window.innerWidth * 0.0625;
            const cols = Math.max(6, Math.ceil(window.innerWidth / tile));
            const rows = Math.max(6, Math.ceil(window.innerHeight / tile) + 1);
            setCount(cols * rows);
        };
        update();
        window.addEventListener("resize", update);
        return () => window.removeEventListener("resize", update);
    }, []);

    const cells = Array.from({ length: count }, (_, i) => i + 1);
    const rays = Array.from({ length: RAYS });

    return (
        <div className="authgrid" aria-hidden="true">
            <div className="authgrid-glow" />
            {cells.map((n) => <div className="authgrid-cell" key={n} />)}
            <div className="authgrid-rays">
                {rays.map((_, i) => (
                    <div className="authgrid-ray" key={i} style={{ animationDelay: `${i * 0.85}s` }} />
                ))}
            </div>
        </div>
    );
}

export default AuthBackground;