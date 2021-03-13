import http from "../http";

export const login = (username, password) =>
  http.post("login", {
    username,
    password,
  });

export const logout = () => http.post("logout");

export const register = (
  username,
  password,
  password_confirmation,
  name,
  email
) =>
  http.post("register", {
    username,
    password,
    password_confirmation,
    name,
    email,
  });
