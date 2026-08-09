function PageHero({

    title,

    subtitle,

    background

}) {

    return (

        <section
            className="page-hero"
            style={{

                backgroundImage: background
                    ? `url(${background})`
                    : "none"

            }}
        >

            <div className="page-overlay"></div>

            <div className="container">

                <div className="page-hero-content">

                    <h1>

                        {title}

                    </h1>

                    {

                        subtitle && (

                            <p>

                                {subtitle}

                            </p>

                        )

                    }

                </div>

            </div>

        </section>

    );

}

export default PageHero;
