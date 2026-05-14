DROP TABLE IF EXISTS project_resources;
DROP TABLE IF EXISTS resources;
DROP TABLE IF EXISTS projects;

CREATE TABLE projects (
    id INT PRIMARY KEY,
    title VARCHAR(120) NOT NULL,
    description TEXT NOT NULL,
    manager VARCHAR(120) NOT NULL,
    location_name VARCHAR(220) NOT NULL,
    latitude DECIMAL(12, 8) NOT NULL,
    longitude DECIMAL(12, 8) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE resources (
    id INT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    resource_type VARCHAR(100) NOT NULL,
    conditions_of_use TEXT NOT NULL
);

CREATE TABLE project_resources (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    resource_id INT NOT NULL,
    CONSTRAINT fk_project_resources_project
        FOREIGN KEY (project_id)
        REFERENCES projects(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_project_resources_resource
        FOREIGN KEY (resource_id)
        REFERENCES resources(id)
        ON DELETE CASCADE
);