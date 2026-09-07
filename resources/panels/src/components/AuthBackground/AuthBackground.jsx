import "../../theme/css/auth-grid.css";

const CELL_GAP = 56;

function AuthBackground() {
    const cells = Array.from({ length: 72 });

    return (
        <div className="authgrid" aria-hidden="true">
            <div className="authgrid-glow" />
            {cells.map((_, i) => <div className="authgrid-cell" key={i} />)}
        </div>
    );
}

export default AuthBackground;
