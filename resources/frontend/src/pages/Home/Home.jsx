import Hero from "../../components/hero/Hero";
import TrustedCompanies from "../../components/trustedCompanies/TrustedCompanies";
import Categories from "../Category/Categories";
import CTA from "../CTA/CTA";
import FeaturedGigs from "../FeaturedGigs/FeaturedGigs";
import Statistics from "../Statistics/Statistics";
import Testimonials from "../Testimonials/Testimonials";
import WhyKhanVerse from "../WhyKhanVerse/WhyKhanVerse";

function Home() {
    return (
        <>
            <Hero />

            <TrustedCompanies />

            <Categories />

            <FeaturedGigs />

            <WhyKhanVerse />

            <Statistics />

            <Testimonials />

            <CTA />
        </>
    );
}

export default Home;
