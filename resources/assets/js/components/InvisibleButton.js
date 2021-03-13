import React from "react";
import PropTypes from "prop-types";

const invisibleButtonStyles = {
  background: "transparent",
  border: "none",
  outline: "0",
};

const IconButton = ({ icon, styles, children, disabled, ...props }) => (
  <button
    {...props}
    style={{
      cursor: disabled ? "default" : "pointer",
      ...invisibleButtonStyles,
      ...styles,
    }}
    disabled={disabled}
  >
    {icon}
    {children}
  </button>
);

IconButton.propTypes = {
  /**
   * Class of the icon. Defines the appearance of the icon button
   */
  icon: PropTypes.node.isRequired,
  /**
   * onClick event handler. The function to be called when the icon is selected
   */
  onClick: PropTypes.func,
  /**
   * CSS style overide
   */
  styles: PropTypes.shape(),
  /**
   * JSX node(s) useful for rendering a label
   */
  children: PropTypes.node,
  /**
   * If true the component will be disabled
   */
  disabled: PropTypes.bool,
};

IconButton.defaultProps = {
  onClick: () => {},
  styles: {},
  children: null,
  disabled: false,
};

export default IconButton;
