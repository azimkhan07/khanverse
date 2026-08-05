import { BrowserRouter, Routes, Route } from "react-router-dom";

import PublicLayout from "../layouts/PublicLayout";
import Home from "../pages/Home/Home";

function AppRoutes() {
    return (
        <BrowserRouter>
            <Routes>
                <Route element={<PublicLayout />}>
                    <Route index element={<Home />} />
                    {/* ya */}
                    {/* <Route path="/" element={<Home />} /> */}
                </Route>
            </Routes>
        </BrowserRouter>
    );
}

export default AppRoutes;
