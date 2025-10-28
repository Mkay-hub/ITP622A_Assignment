-- Active: 1760523181098@@127.0.0.1@3306

CREATE DATABASE socialdb CHARACTER SET utf8mb4;

USE socialdb;

CREATE TABLE socialdb.users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    profile_pic VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE socialdb.posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (id)
);

CREATE TABLE socialdb.messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    text_message TEXT NOT NULL,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users (id),
    FOREIGN KEY (receiver_id) REFERENCES users (id)
);

INSERT INTO
    socialdb.users (
        id,
        username,
        email,
        password_hash,
        created_at
    )
VALUES (
        1,
        'jason_miller',
        'jason.miller@techmail.com',
        '$2y$10$Xf7JkD4rY1pQw9tH6eC2uO3sR5bL8mA0nZxVjF6dT9rPqU2gB4hN6',
        '2025-10-18 08:25:43'
    ),
    (
        2,
        'samantha_lee',
        'samantha.lee@protonmail.com',
        '$2y$10$Rk9dT2xJ5pF8mV4aC1nS6yW3bE7qU9rZ0hL2tN5vG3oK8fD1jP6sH',
        '2025-10-19 10:17:55'
    ),
    (
        3,
        'nathan_olsen',
        'nathan.olsen@codeworks.io',
        '$2y$10$Nf5aH2jK8vD1tP4mS9yR3wG6lB0eX7qJ2cC8zV5uT9pL3rA1nY6oM',
        '2025-10-20 11:48:09'
    ),
    (
        4,
        'melissa_jones',
        'melissa.jones@innovatehub.org',
        '$2y$10$Yg3hQ6tV1pM7sR4wF8bL2dC9jE5xN0kT3aZ1uP6vO8mD2rB7nS4cL',
        '2025-10-21 09:02:34'
    ),
    (
        5,
        'kevin_brown',
        'kevin.brown@devsphere.net',
        '$2y$10$Pj1uD6mE4tH8vQ2wN3aB9rS0yL5cF7kZ2pG1oX8sR4nT6vU3dW9qM',
        '2025-10-22 13:40:12'
    ),
    (
        6,
        'rachel_smith',
        'rachel.smith@datascope.ai',
        '$2y$10$Ew2nJ4yP7rB1sQ5tM8hK9zD0fL6vA3xC2pT7gV5uR9eW8bN4mY1qH',
        '2025-10-23 15:12:07'
    ),
    (
        7,
        'daniel_cooper',
        'daniel.cooper@nextgenlabs.com',
        '$2y$10$Uq8yN5mV3fC7rT1pG9dH2aL0bW4eS6nX7kZ3tJ5oY9vF8uR2cM1qE',
        '2025-10-24 16:28:21'
    ),
    (
        8,
        'laura_patel',
        'laura.patel@brightdata.co',
        '$2y$10$Vn6rL9sH3qT1wP8mK2dA7eX0bF4yC5tN9gJ6vU2oR8zW3fD1pM7cS',
        '2025-10-25 18:03:45'
    ),
    (
        9,
        'ryan_hughes',
        'ryan.hughes@cloudmatrix.dev',
        '$2y$10$Ck5tW8mL2nR3pV6yS9aH1bJ7qX4eD0zT2gN8oF9vY5rP1uM3sK6cE',
        '2025-10-26 09:47:19'
    ),
    (
        10,
        'emma_wilson',
        'emma.wilson@cyberlane.io',
        '$2y$10$Lh2aT5rN9pC7yV4wS6mE3jF0kQ8nD1bG5zU9oR2tM4xY7vA8lP3qH',
        '2025-10-27 14:55:02'
    );

INSERT INTO
    socialdb.posts (
        id,
        user_id,
        content,
        created_at
    )
VALUES (
        1,
        1,
        'Just launched my first web application using Flask! Excited to keep improving the UI next.',
        '2025-10-20 09:30:14'
    ),
    (
        2,
        2,
        'Data preprocessing is done — now running model training using Random Forest. Fingers crossed for accuracy above 90%!',
        '2025-10-21 11:42:09'
    ),
    (
        3,
        3,
        'Currently exploring API integration for an IoT project. MQTT is trickier than I thought!',
        '2025-10-22 15:18:32'
    ),
    (
        4,
        4,
        'Had a productive day debugging the authentication module — learned a lot about JWT tokens!',
        '2025-10-23 08:57:20'
    ),
    (
        5,
        5,
        'Deploying my portfolio site to AWS this week. Hoping for a smooth setup with S3 and CloudFront.',
        '2025-10-23 19:21:47'
    ),
    (
        6,
        6,
        'Just finished cleaning a massive dataset with over 2 million rows. Pandas to the rescue!',
        '2025-10-24 10:05:56'
    ),
    (
        7,
        7,
        'Built a dashboard for visualizing network performance — used Plotly for dynamic charts.',
        '2025-10-25 13:42:38'
    ),
    (
        8,
        8,
        'Experimenting with GPT APIs for an AI chatbot prototype. The responses are surprisingly natural!',
        '2025-10-25 17:26:59'
    ),
    (
        9,
        9,
        'Implemented serverless functions with Firebase today. Definitely speeds up the backend setup!',
        '2025-10-26 10:54:23'
    ),
    (
        10,
        10,
        'Completed my cybersecurity module — next up, penetration testing and ethical hacking basics.',
        '2025-10-27 15:33:17'
    );

INSERT INTO
    socialdb.messages (
        sender_id,
        receiver_id,
        text_message,
        sent_at
    )
VALUES (
        1,
        2,
        'Hey Samantha, just checked out your data model — looks great! Mind if I add a few comments?',
        '2025-10-20 10:12:34'
    ),
    (
        2,
        1,
        'Thanks, Jason! Go ahead, I’d appreciate your input before I finalize the report.',
        '2025-10-20 10:35:10'
    ),
    (
        3,
        5,
        'Kevin, are you available for a quick code review this afternoon?',
        '2025-10-21 14:42:28'
    ),
    (
        5,
        3,
        'Sure Nathan, I’ll be free around 15:30. Send me your GitHub link.',
        '2025-10-21 15:03:47'
    ),
    (
        4,
        6,
        'Rachel, can you share your notes on the last data visualization lecture?',
        '2025-10-22 08:51:55'
    ),
    (
        6,
        4,
        'Of course! I’ll email you the slides and example dashboard templates.',
        '2025-10-22 09:05:11'
    ),
    (
        7,
        8,
        'Laura, that AI chatbot demo was impressive. Did you use LangChain for it?',
        '2025-10-23 11:44:29'
    ),
    (
        8,
        7,
        'Thanks Daniel! Yes, I integrated LangChain with a GPT API for context handling.',
        '2025-10-23 11:58:36'
    ),
    (
        9,
        10,
        'Emma, can you join the meeting later to discuss the cloud deployment strategy?',
        '2025-10-24 13:26:12'
    ),
    (
        10,
        9,
        'Absolutely, Ryan. I’ll review the current setup before the meeting.',
        '2025-10-24 13:49:03'
    );

/*SELECT * FROM messages;*/