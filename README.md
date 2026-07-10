# Changelog (Plugin)

Add a changelog to your website.

## API Usage

This plugin provides a secure API endpoint to create new changelog updates dynamically.

### Create a Changelog Update
`POST /api/changelog/updates`

**Headers:**
- `Authorization: Bearer <token>`
- `Content-Type: application/json`

*(Note: API Tokens can be configured from the plugin's settings. Tokens should be long random strings, >=32 characters, and separated by commas if multiple.)*

**Payload Example:**
```json
{
  "category_id": 1,
  "name": "v1.2.3",
  "description": "Added new economy commands and fixed login bug."
}
```

**Success Response (201 Created):**
```json
{
  "message": "Update created successfully.",
  "data": {
    "id": 10,
    "title": "v1.2.3",
    "content": "Added new economy commands and fixed login bug.",
    "category": {
      "id": 1,
      "name": "General"
    },
    "created_at": "2023-10-10T12:00:00+00:00"
  }
}
```

**Error Responses:**
- `401 Unauthorized` - If the `Authorization` token is missing or invalid.
- `422 Unprocessable Entity` - If validation fails (e.g., missing fields, string limits exceeded).
- `429 Too Many Requests` - The API restricts creations to 60 requests per minute.
