import React, { useContext } from "react";
import {
  Nav,
  Form,
  FormControl,
  Button,
  InputGroup,
  Navbar,
  NavDropdown,
} from "react-bootstrap";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faSearch } from "@fortawesome/free-solid-svg-icons";
import AppContext from "../AppContext";
import { logout } from "../actions/authActions";

const NavigationBar = () => {
  const { user, setUser } = useContext(AppContext);
  const handleLogoutClick = () => {
    logout().then(() => setUser(null));
  };
  return (
    <Navbar bg="light" expand="lg">
      <Navbar.Brand href="/">Collaboro</Navbar.Brand>
      <Navbar.Toggle aria-controls="app-navbar" />
      <Navbar.Collapse id="app-navbar">
        <Nav className="mr-auto">
          <Nav.Link href="/questions">Latest questions</Nav.Link>
        </Nav>
        <Form inline className="ml-auto mr-auto">
          <InputGroup className="top-search-bar-input-group">
            <FormControl type="text" placeholder="Search for a question" />
            <InputGroup.Append>
              <Button type="submit">
                <FontAwesomeIcon icon={faSearch} />
              </Button>
            </InputGroup.Append>
          </InputGroup>
        </Form>
        <Nav>
          {user ? (
            <NavDropdown title={user.name} id="basic-nav-dropdown">
              <NavDropdown.Item href="#action/3.1">Profile</NavDropdown.Item>
              <NavDropdown.Divider />
              <NavDropdown.Item onClick={handleLogoutClick}>
                Logout
              </NavDropdown.Item>
            </NavDropdown>
          ) : (
            <Nav.Link href="/login">Login</Nav.Link>
          )}
        </Nav>
      </Navbar.Collapse>
    </Navbar>
  );
};

export default NavigationBar;
