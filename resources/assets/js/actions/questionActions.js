import http from "../http";

export const loadQuestions = () => http.get("questions");

export const loadQuestion = (id) => http.get(`questions/${id}`);
