
## API Reference

#### Login. Returns JWT token, if authentication successfull.

```http
  POST /login
```

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `email`   | `email`  | **Required**. Users email  |
| `password`| `string` | **Required**. Users password  |

#### Get item. Return randomly selected prize from prize storage.
#### User must be authenticated.

```http
  GET /get-random-prize
```

| Parameter | Type     | Description                       |
| :-------- | :------- | :-------------------------------- |
| `jwt`     | `string` | **Required**. JWT Token |


