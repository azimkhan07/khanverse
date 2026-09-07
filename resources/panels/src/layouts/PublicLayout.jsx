import { Outlet } from "react-router-dom";

import Navbar from "../components/navbar/Navbar";
import Footer from "../components/footer/Footer";
import CompareTray from "../components/compare/CompareTray";

function PublicLayout() {
    return (
        <>
            <Navbar />
            <Outlet />
            <Footer />
            <CompareTray />
        </>
    );
}

export default PublicLayout;