import { BrowserRouter, Routes, Route } from "react-router-dom";

import PublicLayout from "../layouts/PublicLayout";
import GuestLayout from "../layouts/GuestLayout";

import Home from "../pages/Home/Home";

import Login from "../pages/Auth/Login";
import Register from "../pages/Auth/Register";
import ForgotPassword from "../pages/Auth/ForgotPassword";
import ResetPassword from "../pages/Auth/ResetPassword";
import VerifyEmail from "../pages/Auth/VerifyEmail";
import About from "../pages/About/About";
import FAQ from "../pages/FAQ/FAQ";
import NotFound from "../pages/NotFound/NotFound";
import CookiePolicy from "../pages/CookiePolicy/CookiePolicy";
import TermsConditions from "../pages/TermsConditions/TermsConditions";
import PrivacyPolicy from "../pages/PrivacyPolicy/PrivacyPolicy";
import Careers from "../pages/Careers/Careers";
import Blog from "../pages/Blog/Blog";
import { Contact } from "lucide-react";
import Pricing from "../pages/Pricing/Pricing";

function AppRoutes() {
    return (
        <BrowserRouter>
            <Routes>
                {/* Public */}
                <Route element={<PublicLayout />}>
                    <Route index element={<Home />} />
                    <Route path="/about" element={<About />} />
                    <Route path="/pricing" element={<Pricing />} />
                    <Route path="/faq" element={<FAQ />} />
                    <Route path="/contact" element={<Contact />} />
                    <Route path="/blog" element={<Blog />} />
                    {/* <Route path="/blog/:slug" element={<BlogDetails />} /> */}
                    <Route path="/careers" element={<Careers />} />
                    <Route path="/privacy-policy" element={<PrivacyPolicy />} />
                    <Route path="/terms-conditions" element={<TermsConditions />} />
                    <Route path="/cookie-policy" element={<CookiePolicy />} />
                </Route>
                {/* Guest */}
                <Route element={<GuestLayout />}>
                    <Route path="/login" element={<Login />} />
                    <Route path="/register" element={<Register />} />
                    <Route path="/forgot-password" element={<ForgotPassword />} />
                    <Route path="/reset-password/:token" element={<ResetPassword />} />
                    <Route path="/verify-email" element={<VerifyEmail />} />
                </Route>
            </Routes>
        </BrowserRouter>
    );
}

export default AppRoutes;
