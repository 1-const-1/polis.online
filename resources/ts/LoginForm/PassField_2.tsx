import React from "react"

const PassField_2: React.FC<{
  minLen: number,
  pass: string,
}> = ({minLen, pass}) => {
  const errMsg = "Inputs are not the same";

  const [isErr, setIsErr] = React.useState(false);

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>): void => {
    const rpass = e.target.value;
    pass !== rpass ? setIsErr(true) : setIsErr(false);
  }

  return (
    <>
      <div>
        <label htmlFor="pass_2">Repeat password</label>
        <input 
          id="pass_2" 
          type="password" 
          name="rpass"
          onChange={handleChange}
          minLength={minLen} />
      </div>
      {isErr && <div>{errMsg}</div>}
    </>
  );
}

export default PassField_2;