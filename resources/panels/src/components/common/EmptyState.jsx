function EmptyState({

    title = "No Data Found",

    description = "There is nothing to display."

}) {

    return (

        <div className="empty-state">

            <h3>

                {title}

            </h3>

            <p>

                {description}

            </p>

        </div>

    );

}

export default EmptyState;
