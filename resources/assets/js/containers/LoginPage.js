import React, { useState, useContext } from "react";
import { Form, Button, Card, Alert } from "react-bootstrap";
import { useHistory } from "react-router-dom";
import AppContext from "../AppContext";
import { login } from "../actions/authActions";

const LoginPage = () => {
  const [username, setUsername] = useState("");
  const [password, setPassword] = useState("");
  const [loggingIn, setLoggingIn] = useState(false);
  const [loginFailed, setLoginFailed] = useState(false);
  const { setUser } = useContext(AppContext);
  const history = useHistory();
  const handleLoginClick = () => {
    setLoggingIn(true);
    setLoginFailed(false);

    login(username, password).then(
      (response) => {
        setUser(response.data.user);
        history.push("/");
      },
      () => {
        setLoginFailed(true);
        setLoggingIn(false);
      }
    );
  };
  return (
    <Card
      style={{
        width: "300px",
        margin: "50px auto",
        padding: "20px",
      }}
    >
      {loginFailed && (
        <Alert variant="danger">
          Login failed, please check your credentials and try again
        </Alert>
      )}
      <Form>
        <Form.Group controlId="formUsername">
          <Form.Label>Username</Form.Label>
          <Form.Control
            type="text"
            placeholder="Enter username"
            value={username}
            onChange={(event) => setUsername(event.target.value)}
          />
        </Form.Group>
        <Form.Group controlId="formPassword">
          <Form.Label>Password</Form.Label>
          <Form.Control
            type="password"
            placeholder="Password"
            value={password}
            onChange={(event) => setPassword(event.target.value)}
          />
        </Form.Group>
        <Form.Text>
          <a href="/register">Create an account</a>
        </Form.Text>
        <Form.Text>
          <a href="/reset">Forgotten password</a>
        </Form.Text>
        <Button
          variant="primary"
          type="button"
          block
          onClick={handleLoginClick}
          disabled={loggingIn}
        >
          Login
        </Button>
      </Form>
    </Card>
  );
};

export default LoginPage;
