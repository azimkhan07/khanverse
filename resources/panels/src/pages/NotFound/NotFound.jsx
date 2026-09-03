import { Link } from "react-router-dom";

import "../../theme/css/not-found.css";

function NotFound() {

    return (

        <section className="not-found">

            <div className="container">

                <div className="not-found-content">

                    <h1>

                        404

                    </h1>

                    <h2>

                        Oops! Page Not Found

                    </h2>

                    <p>

                        The page you're looking for doesn't exist or has been moved.

                    </p>

                    <Link
                        to="/"
                        className="not-found-btn"
                    >

                        Back To Home

                    </Link>

                </div>

            </div>

        </section>

    );

}

export default NotFound;
