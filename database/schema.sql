-- Tempest Phase 2 database schema
-- Creates project, resource and project-resource relationship tables.
-- project_resources is a junction table that supports many-to-many allocation
-- between construction projects and equipment resources.

-- Drop junction table first because it depends on projects and resources.
DROP TABLE IF EXISTS project_resources;
DROP TABLE IF EXISTS resources;
DROP TABLE IF EXISTS projects;

-- Stores construction project records and location coordinates.
CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(120) NOT NULL,
    description TEXT NOT NULL,
    location_name VARCHAR(160) NOT NULL,
    latitude DECIMAL(10, 7) NOT NULL,
    longitude DECIMAL(10, 7) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Stores construction equipment/resource types used by projects.
CREATE TABLE resources (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    resource_type VARCHAR(80) NOT NULL
);

-- Links projects to allocated resources and stores the quantity required.
CREATE TABLE project_resources (
    project_id INT NOT NULL,
    resource_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    PRIMARY KEY (project_id, resource_id),
    CONSTRAINT fk_project_resources_project
        FOREIGN KEY (project_id)
        REFERENCES projects(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_project_resources_resource
        FOREIGN KEY (resource_id)
        REFERENCES resources(id)
        ON DELETE CASCADE
);