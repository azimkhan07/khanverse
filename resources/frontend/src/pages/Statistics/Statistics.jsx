import "../../theme/css/statistics.css";
import { statistics } from "../../components/Statistics/statisticsData";

function Statistics() {

    return (

        <section className="statistics">

            <div className="container">

                <div className="section-heading">

                    <span>Our Achievements</span>

                    <h2>

                        Trusted by Businesses Worldwide

                    </h2>

                    <p>

                        Thousands of clients trust KhanVerse to hire the best freelancers and grow their business.

                    </p>

                </div>

                <div className="statistics-grid">

                    {

                        statistics.map((item) => (

                            <div
                                className="statistics-card"
                                key={item.id}
                            >

                                <h2>

                                    {item.number}

                                </h2>

                                <p>

                                    {item.title}

                                </p>

                            </div>

                        ))

                    }

                </div>

            </div>

        </section>

    );

}

export default Statistics;
