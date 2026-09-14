import { AuthProvider } from "./contexts/AuthContext";
import AppRoutes from "./routes/AppRoutes";
import DialogHost from "./shared/components/Dialog";
import LoginProfilePrompt from "./shared/components/LoginProfilePrompt";

function App() {
    return (
        <AuthProvider>
            <AppRoutes />
            <LoginProfilePrompt />
            <DialogHost />
        </AuthProvider>
    );
}

export default App;
