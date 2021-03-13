import React, { useState } from "react";
import { BrowserRouter, Switch, Redirect, Route } from "react-router-dom";
import { Container } from "react-bootstrap";
import AppContext from "../AppContext";
import QuestionsPage from "./QuestionsPage";
import LoginPage from "./LoginPage";
import RegisterPage from "./RegisterPage";
import NavigationBar from "../components/NavigationBar";
import QuestionDetailPage from "./QuestionDetailPage";

const Collaboro = () => {
  const [user, setUser] = useState(null);
  return (
    <AppContext.Provider value={{ user, setUser }}>
      <NavigationBar />
      <Container fluid>
        <BrowserRouter>
          <Switch>
            <Redirect path="/" exact to="/questions" />
            <Route path="/questions" component={QuestionsPage} />
            <Route path="/question/:id" component={QuestionDetailPage} />

            {!user && (
              <>
                <Route path="/login" component={LoginPage} />
                <Route path="/register" component={RegisterPage} />
              </>
            )}
          </Switch>
        </BrowserRouter>
      </Container>
    </AppContext.Provider>
  );
};

export default Collaboro;
