import "../../theme/css/testimonials.css";
import { testimonials } from "../../components/Testimonials/testimonialsData";
import { Star, Quote } from "lucide-react";

function Testimonials() {

    return (

        <section className="testimonials">

            <div className="container">

                <div className="section-heading">

                    <span>Testimonials</span>

                    <h2>

                        What Our Clients Say

                    </h2>

                    <p>

                        Thousands of businesses trust KhanVerse to hire top freelancers from around the world.

                    </p>

                </div>

                <div className="testimonial-grid">

                    {

                        testimonials.map((item)=>(

                            <div
                                className="testimonial-card"
                                key={item.id}
                            >

                                <Quote
                                    className="quote-icon"
                                    size={36}
                                />

                                <div className="testimonial-stars">

                                    {

                                        [...Array(item.rating)].map((_,index)=>(

                                            <Star
                                                key={index}
                                                size={18}
                                                fill="#fbbf24"
                                                color="#fbbf24"
                                            />

                                        ))

                                    }

                                </div>

                                <p className="testimonial-review">

                                    "{item.review}"

                                </p>

                                <div className="testimonial-user">

                                    <img
                                        src={item.image}
                                        alt={item.name}
                                    />

                                    <div>

                                        <h4>{item.name}</h4>

                                        <span>{item.role}</span>

                                    </div>

                                </div>

                            </div>

                        ))

                    }

                </div>

            </div>

        </section>

    );

}

export default Testimonials;
