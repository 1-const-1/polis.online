import "@testing-library/jest-dom";
import {render, screen} from "@testing-library/react";
import React from "react";
import App from "./App";

test("Check root", () => {
  render(<App />);
  expect(screen.queryByText("Application...")).toBeInTheDocument();
});