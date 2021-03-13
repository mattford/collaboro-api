import axios from "axios";

const http = axios.create({
  baseURL: "/api",
});

export const authorizationTokenInterceptor = (config) => {
  const token = localStorage.getItem("collaboro_access_token");

  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }

  return config;
};

http.interceptors.request.use(authorizationTokenInterceptor, (error) =>
  Promise.reject(error)
);

export default http;
