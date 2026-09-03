import { AuthProvider } from "./contexts/AuthContext";
import AppRoutes from "./routes/AppRoutes";
import DialogHost from "./shared/components/Dialog";

function App() {
    return (
        <AuthProvider>
            <AppRoutes />
            <DialogHost />
        </AuthProvider>
    );
}

export default App;
