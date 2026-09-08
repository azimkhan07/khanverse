import AuthBackground from "../../components/AuthBackground/AuthBackground";

function AuthShell({ children, wide = false }) {
    return (
        <section className="auth">
            <AuthBackground />
            <div className={`auth-card${wide ? " auth-card--wide" : ""}`}>
                {children}
            </div>
        </section>
    );
}

export default AuthShell;