function SectionHeading({
    tag,
    title,
    subtitle,
    center = true
}) {

    return (

        <div
            className={`section-heading ${center ? "text-center" : ""}`}
        >

            {

                tag && (

                    <span className="section-tag">

                        {tag}

                    </span>

                )

            }

            {

                title && (

                    <h2>

                        {title}

                    </h2>

                )

            }

            {

                subtitle && (

                    <p>

                        {subtitle}

                    </p>

                )

            }

        </div>

    );

}

export default SectionHeading;
